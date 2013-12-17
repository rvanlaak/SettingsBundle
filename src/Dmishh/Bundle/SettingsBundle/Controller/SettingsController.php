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

use Dmishh\Bundle\SettingsBundle\Entity\UserInterface;
use Journalist\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SettingsController extends Controller
{
    /**
     * @Route("/manage/{user_id}", name="dmishh_settings", requirements={"user_id" = "\d+"})
     */
    public function manageSettingsAction(Request $request, $user_id = null)
    {
        $user = $this->getUserObject($user_id);
        $this->verifyCredentials($user);

        $form = $this->createForm('dmishh_settings', $this->get('settings_manager')->all($user));

        if ($request->isMethod('post')) {
            $form->bind($request);

            if ($form->isValid()) {

                $this->get('settings_manager')->setMany($form->getData(), $user);
                $this->get('session')->getFlashBag()->add('success', 'Settings were successfully updated!');

                return $this->redirect($request->getUri());
            } else {

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

    /**
     * @param int $userId
     * @return UserInterface|null
     */
    protected function getUserObject($userId = null)
    {
        $userClass = $this->container->getParameter('settings_manager.user_class');
        return $userId === null ? null : $this->get('doctrine')->getManager()->getRepository($userClass)->find($userId);
    }

    /**
     * @param UserInterface $user
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function verifyCredentials(UserInterface $user = null)
    {
        $securitySettings = $this->container->getParameter('settings_manager.security');

        if (!empty($securitySettings['manage_settings_role'])) {
            if ($user === null) {
                if (!$this->get('security.context')->isGranted($securitySettings['manage_settings_role'])) {
                    throw new AccessDeniedException('You are not allowed to edit global settings');
                }
            } else {
                if (!$this->get('security.context')->isGranted($securitySettings['manage_settings_role']) &&
                    !($securitySettings['users_can_manage_own_settings'] && $this->getUser() == $user)
                ) {
                    throw new AccessDeniedException('You are not allowed to edit user settings');
                }
            }
        }
    }
}
