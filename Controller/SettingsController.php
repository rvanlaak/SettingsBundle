<?php

namespace Dmishh\SettingsBundle\Controller;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;
use Dmishh\SettingsBundle\Form\Type\SettingsType;
use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsController extends AbstractController
{
    private ?string $securityRole;

    private bool $securityManageOwnSettings;

    private TranslatorInterface $translator;

    private SettingsManagerInterface $settingsManager;

    private string $template;

    public function __construct(
        TranslatorInterface $translator,
        SettingsManagerInterface $settingsManager,
        string $template,
        bool $securityManageOwnSettings,
        ?string $securityRole
    ) {
        $this->translator = $translator;
        $this->settingsManager = $settingsManager;
        $this->template = $template;
        $this->securityManageOwnSettings = $securityManageOwnSettings;
        $this->securityRole = $securityRole;
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
        $user = $this->getUser();

        if (null === $user) {
            throw new AccessDeniedException($this->translator->trans('must_be_logged_in_to_edit_own_settings', [], 'settings'));
        }

        if (!$this->securityManageOwnSettings) {
            throw new AccessDeniedException($this->translator->trans('not_allowed_to_edit_own_settings', [], 'settings'));
        }

        if (!$user instanceof SettingsOwnerInterface) {
            // For this to work the User entity must implement SettingsOwnerInterface
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
