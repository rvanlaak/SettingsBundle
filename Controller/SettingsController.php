<?php

namespace Dmishh\SettingsBundle\Controller;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;
use Dmishh\SettingsBundle\Form\Type\SettingsType;
use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsController extends AbstractController
{
    /**
     * @var string|null
     */
    private $securityRole;

    /**
     * @var bool
     */
    private $securityManageOwnSettings;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var string
     */
    private $template;

    public function __construct(
        TranslatorInterface $translator,
        SettingsManagerInterface $settingsManager,
        string $template,
        bool $securityManageOwnSettings,
        ?string $securityRole,
        TokenStorageInterface $tokenStorage
    ) {
        $this->translator = $translator;
        $this->settingsManager = $settingsManager;
        $this->template = $template;
        $this->securityManageOwnSettings = $securityManageOwnSettings;
        $this->securityRole = $securityRole;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws AccessDeniedException
     */
    public function manageGlobalAction(Request $request): Response
    {
        if (null !== $this->securityRole && !$this->isGranted($this->securityRole)) {
            throw new AccessDeniedException($this->translator->trans('not_allowed_to_edit_global_settings', [], 'settings'));
        }

        return $this->manage($request);
    }

    /**
     * @throws AccessDeniedException
     */
    public function manageOwnAction(Request $request): Response
    {
        if (null === $this->tokenStorage->getToken()) {
            throw new AccessDeniedException($this->translator->trans('must_be_logged_in_to_edit_own_settings', [], 'settings'));
        }

        if (!$this->securityManageOwnSettings) {
            throw new AccessDeniedException($this->translator->trans('not_allowed_to_edit_own_settings', [], 'settings'));
        }

        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof SettingsOwnerInterface) {
            //For this to work the User entity must implement SettingsOwnerInterface
            throw new AccessDeniedException();
        }

        return $this->manage($request, $user);
    }

    protected function manage(Request $request, ?SettingsOwnerInterface $owner = null): Response
    {
        $form = $this->createForm(SettingsType::class, $this->settingsManager->all($owner));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->settingsManager->setMany($form->getData(), $owner);
            $this->addFlash('success', $this->translator->trans('settings_updated', [], 'settings'));

            return $this->redirect($request->getUri());
        }

        return $this->render($this->template, [
            'settings_form' => $form->createView(),
        ]);
    }
}
