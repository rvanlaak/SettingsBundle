<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class SettingsController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function manageGlobalAction(Request $request)
    {
        $securitySettings = $this->container->getParameter('settings_manager.security');
        if (!empty($securitySettings['manage_global_settings_role']) && !$this->get('security.context')->isGranted($securitySettings['manage_global_settings_role'])) {
            throw new AccessDeniedException($this->container->get('translator')->trans('not_allowed_to_edit_global_settings', array(), 'settings'));
        }

        return $this->manage($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function manageOwnAction(Request $request)
    {
        if (!$this->get('security.context')->getToken()) {
            throw new AccessDeniedException($this->get('translator')->trans('must_be_logged_in_to_edit_own_settings', array(), 'settings'));
        }

        $securitySettings = $this->container->getParameter('settings_manager.security');
        if (!$securitySettings['users_can_manage_own_settings']) {
            throw new AccessDeniedException($this->get('translator')->trans('not_allowed_to_edit_own_settings', array(), 'settings'));
        }

        return $this->manage($request, $this->get('security.context')->getToken()->getUser());
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function manage(Request $request, UserInterface $user = null)
    {
        $form = $this->createForm('settings_management', $this->get('settings_manager')->all($user));

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('settings_manager')->setMany($form->getData(), $user);
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('settings_updated', array(), 'settings'));

                return $this->redirect($request->getUri());
            }
        }

        return $this->render(
            $this->container->getParameter('settings_manager.template'),
            array(
                'settings_form' => $form->createView(),
                'layout' => $this->container->getParameter('settings_manager.layout'),
            )
        );
    }
}
