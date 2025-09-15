<?php

/*
 * Luthier CI
 *
 * (c) 2018 Ingenia Software C.A
 *
 * This file is part of Luthier CI, a plugin for CodeIgniter 3. See the LICENSE
 * file for copyright information and license details
 */

use Luthier\Auth\UserInterface;
use Luthier\Auth\UserProviderInterface;
use Luthier\Auth\Exception\UserNotFoundException;
use Luthier\Auth\Exception\InactiveUserException;
use Luthier\Auth\Exception\UnverifiedUserException;

/**
 * SimpleAuth user provider
 * 
 * @author Anderson Salas <anderson@ingenia.me>
 */
class MyUserProvider implements UserProviderInterface
{
    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\UserProviderInterface::getUserClass()
     */
    public function getUserClass()
    {
        return 'MyUser';
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\UserProviderInterface::loadUserByUsername()
     */
    final public function loadUserByUsername($username, $password = null)
    {
        $db = ci()->adodb->init();

        $username = $db->qstr($username);

        $tbl_user = config_item('simpleauth_users_table');
        $field_user = config_item('simpleauth_username_col');

        $sql = "SELECT a.asid, a.pid, a.username, a.user_group, a.user_module, a.user_gudang
                    , a.user_otorisasi, a.user_approval, a.last_login, a.is_admin, a.is_lock
                    , a.is_active, b.nama_lengkap,  b.jenis_kelamin AS sex, a.userpass
                    , b.is_aktif, a.last_login_bid
                FROM {$tbl_user} a
                JOIN person b ON b.pid = a.pid
		WHERE b.is_aktif = 't' -- AND b.is_dead = 'f' AND b.is_discharged = 'f' 
			AND a.{$field_user} = $username";
        $user = $db->Execute($sql);



        if (empty($user) || ($password !== null && !$this->verifyPassword($password, $user->fields[config_item('simpleauth_password_col')])))
        {
            throw new UserNotFoundException();
        }

        unset($user->fields[config_item('simpleauth_password_col')]);

        $userClass = $this->getUserClass();

        $roles = $user->fields[config_item('simpleauth_role_col')];

        $user_group = $user->fields['user_group'];

        $permissions = [];

        if (config_item('simpleauth_enable_acl') === true && $roles != 't')
        {
            $sql = "SELECT b.*
                    FROM role_group a, menus b
                    WHERE b.mid IN (SELECT UNNEST(STRING_TO_ARRAY(a.role_mid, ',')::INTEGER[]))
                        AND a.rgid IN (SELECT UNNEST(STRING_TO_ARRAY('$user_group', ',')::INTEGER[]))
                    ORDER BY b.level, b.urutan";
            $databaseUserPermissions = $db->Execute($sql);

            if (!$databaseUserPermissions->EOF)
            {
                foreach ($databaseUserPermissions as $permission)
                {
                    $permission = (object) $permission;

                    $permissionName = '';
                    Auth_library::walkUpPermission($permission->mid, $permissionName);
                    $permissions[$permission->mid] = $permissionName;
                }
            }
        }

        $user_data = (object) $user->fields;

        return new $userClass($user_data, $roles, $permissions);
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\UserProviderInterface::checkUserIsActive()
     */
    final public function checkUserIsActive(UserInterface $user)
    {
        if ($user->getEntity()->{config_item('simpleauth_active_col')} == 'f')
        {
            throw new InactiveUserException();
        }
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\UserProviderInterface::checkUserIsVerified()
     */
    final public function checkUserIsVerified(UserInterface $user)
    {
        /*$enableCheck = config_item('simpleauth_enable_email_verification')  === TRUE &&
                       config_item('simpleauth_enforce_email_verification') === TRUE;

        if (!$enableCheck)
        {
            return;
        }

        if ($user->getEntity()->{config_item('simpleauth_verified_col')} == 0)
        {
            throw new UnverifiedUserException();
        }*/

        // Cek Assign Branch
        $countAvailableBranch = Modules::countUserAvailableBranch($user->pid);

        if ($countAvailableBranch == 0 && isMultiTenants() == 't')
        {
            throw new UnverifiedUserException();
        }
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\UserProviderInterface::hashPassword()
     */
    public function hashPassword($password)
    {
        return CreatePassword($password, PASSWORD_DEFAULT);
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\UserProviderInterface::verifyPassword()
     */
    public function verifyPassword($password, $hash)
    {
        return CheckPassword($password, $hash);
    }
}
