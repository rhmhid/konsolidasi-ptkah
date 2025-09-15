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

/**
 * SimpleAuth User class
 * 
 * @author Anderson Salas <anderson@ingenia.me>
 */
class MyUser implements UserInterface
{
    /**
     * @var object
     */
    private $user;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $permissions;

    // koneksi db
    private static $db;

    private $_token_table;

    // parsing menu
    private static $menus;

    /**
     * @param object $entity
     * @param array  $roles
     * @param array  $permissions
     */
    public function __construct($entity, $roles, $permissions)
    {
        $this->user        = $entity;
        $this->roles       = $roles;
        $this->permissions = $permissions;

        self::$db = ci()->adodb->init();

        $this->_token_table = config_item('simpleauth_users_token_table');
    }

    public function __get($name)
    {
        if(isset($this->getEntity()->{$name}))
        {
            return $this->getEntity()->{$name};
        }
    }

    public function getEntity()
    {
        return $this->user;
    }

    public function getUsername()
    {
        return $this->user->username;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getAdminCaption ()
    {
        if ($this->pid == 1) $caption = 'S.Admin';
        elseif ($this->is_admin == 't') $caption = 'Admin';
        else $caption = '';

        return $caption;
    }

    public function SetMenu ()
    {
        // self::$db->debug = true;

        $is_admin = $this->user->is_admin;

        // Start Generate Module
        if ($is_admin != 't')
        {
            $user_perm = implode(',', array_keys($this->permissions));

            if ($user_perm != '') $addsql = "AND a.mid IN (SELECT UNNEST(STRING_TO_ARRAY('$user_perm', ',')::INTEGER[]) AS mid)";
            else $addsql = "AND a.mid = -1";

            $addsql .= " AND a.super_admin = 'f'";
        }
        else $addsql = '';

        $sql = "SELECT a.*, b.title AS parent_title
                FROM menus a, menus b
                WHERE b.mid = a.parent_mid AND a.is_aktif = 't' AND a.is_display = 't' AND a.level = 2 $addsql
                ORDER BY b.urutan, a.urutan";
        $rs_module = self::$db->Execute($sql);

        if (!$rs_module->EOF)
        {
            $old_parent_mid = '';
            foreach ($rs_module as $key => $row)
            {
                $row = (object) $row;

                if ($old_parent_mid != $row->parent_mid && strtolower($row->parent_title) != 'pengaduan')
                {
                    self::$menus .= '<!--begin:Menu item-->
                                    <div class="menu-item pt-5">
                                        <!--begin:Menu content-->
                                        <div class="menu-content">
                                            <span class="menu-heading fw-bold text-uppercase fs-7">'.$row->parent_title.'</span>
                                        </div>
                                        <!--end:Menu content-->
                                    </div>
                                    <!--end:Menu item-->';
                }

                if ($is_admin != 't')
                {
                    if ($user_perm != '') $addsql = "AND mid IN (SELECT UNNEST(STRING_TO_ARRAY('$user_perm', ',')::INTEGER[]) AS mid)";
                    else $addsql = "AND mid = -1";

                    $addsql .= " AND super_admin = 'f'";
                }
                else $addsql = '';

                $sql = "SELECT * FROM menus WHERE is_aktif = 't' AND is_display = 't' AND level > 2 AND group_mid = ? $addsql ORDER BY urutan";
                $rs_submodule = self::$db->Execute($sql, array($row->mid));

                if (collect($rs_submodule)->count() > 0)
                {
                    $data_submodule = array();

                    foreach ($rs_submodule as $mid => $data)
                    {
                        $data = (object) $data;

                        $data_submodule[$data->parent_mid][] = $data;
                    }

                    $submodule_data = $this->_generate_submodule($data_submodule, $row->mid);
                    if ($submodule_data)
                    {
                        $subdata = 'data-kt-menu-trigger="click"';

                        $subclass = 'menu-accordion';

                        $href_action = '';

                        $subtag = 'span';

                        $subarrow = '<span class="menu-arrow"></span>';

                        $submodule = '<!--begin:Menu sub-->
                                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                                        <!--begin:Menu item-->
                                        '.$submodule_data.'
                                        <!--end:Menu item-->
                                    </div>
                                    <!--end:Menu sub-->';
                    }
                }
                else
                {
                    $subdata = '';
                    $subclass = '';
                    $subtag = 'a';
                    $href_action = 'href="'.route($row->url).'"';
                    $subarrow = '';
                    $submodule = '';
                }

                self::$menus .= '<!--begin:Menu item-->
                                <div '.$subdata.' class="menu-item '.$subclass.'">
                                    <!--begin:Menu link-->
                                    <'.$subtag.' class="menu-link" '.$href_action.'>
                                        <span class="menu-icon">
                                            <i class="las '.$row->icon.' fs-2 me-0"></i>
                                        </span>
                                        <span class="menu-title">'.$row->title.'</span>
                                        '.$subarrow.'
                                    </'.$subtag.'>
                                    <!--end:Menu link-->

                                    '.$submodule.'
                                </div>
                                <!--end:Menu item-->';

                $old_parent_mid = $row->parent_mid;
            }
        }

        return self::$menus;
    }

    private function _generate_submodule ($data_submodule, $parent = '') /*{{{*/
    {
        $submodule = '';

        if (collect($data_submodule[$parent])->count() > 0)
        {
            foreach ($data_submodule[$parent] as $v)
            {
                if ($v->is_header == 't')
                {
                    $sub = '<div class="menu-sub menu-sub-accordion menu-active-bg">
                                '.$this->_generate_submodule($data_submodule, $v->mid).'
                            </div>';

                    $subdata = 'data-kt-menu-trigger="click"';
                    $subclass = 'menu-accordion';
                    $subtag = 'span';
                    $subaction = '';
                    $subspan = '<span class="menu-arrow"></span>';
                }
                else
                {
                    $sub = '';
                    $subdata = '';
                    $subclass = '';
                    $subtag = 'a';
                    $subaction = 'href="'.route($v->url).'"';
                    $subspan = '';
                }

                $submodule .= '<div '.$subdata.' class="menu-item '.$subclass.'">
                                    <'.$subtag.' class="menu-link" '.$subaction.'>
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">'.$v->title.'</span>
                                        '.$subspan.'
                                    </'.$subtag.'>

                                    '.$sub.'
                                </div>';
            }
        }

        return $submodule;
    } /*}}}*/

    public function generateAccessToken (string $name)
    {
        $tokensData = array(
            'token_id'      => $this->user->asid,
            'token_name'    => $name,
            'token'         => hash('sha256', $rawToken = bin2hex(random_bytes(64))),
            'last_used_at'  => time(),
            'expired_at'    => time() + config_item('simpleauth_users_token_expired')
        );

        $sql = "SELECT * FROM {$this->_token_table} WHERE 1 = 2";
        $rs = self::$db->Execute($sql);
        $sqli = self::$db->GetInsertSql($rs, $tokensData);
        $ok = self::$db->Execute($sqli);

        $tokens = (object) array(
            'raw_token' => $rawToken,
            'token'     => $tokensData['token']
        );

        return $tokens;
    }

    /**
     * Given the token, will retrieve the token to
     * verify it exists, then delete it.
     *
     * @return mixed
     */
    public function revokeAccessToken (string $token)
    {
        $sql = "DELETE FROM {$this->_token_table} WHERE token_id = ? AND token = ?";
        $tokens = self::$db->Execute($sql, array($this->user->asid, hash('sha256', $token)));

        return $tokens;
    }

    public function revokeAccessTokenName (string $name)
    {
        $sql = "DELETE FROM {$this->_token_table} WHERE token_id = ? AND token_name = ?";
        $tokens = self::$db->Execute($sql, array($this->user->asid, $name));

        return $tokens;
    }

    /**
     * Revokes all access tokens for this user.
     *
     * @return mixed
     */
    public function revokeAllAccessTokens ()
    {
        $sql = "DELETE FROM {$this->_token_table} WHERE token_id = ?";
        $tokens = self::$db->Execute($sql, array($this->user->asid));

        return $tokens;
    }

    /**
     * Retrieves all personal access tokens for this user.
     *
     * @return array
     */
    public function accessTokens(): array
    {
        $sql = "SELECT * FROM {$this->_token_table} WHERE token_id = ?";
        $rs = self::$db->Execute($sql, array($this->user->asid));

        $tokens = [];

        while (!$rs->EOF)
        {
            $tokens[] = FieldsToObject($rs->fields);

            $rs->MoveNext();
        }

        return $tokens;
    }

    /**
     * Given a raw token, will hash it and attemp to
     * locate it within the system.
     *
     * @return AccessToken|null
     */
    public function getAccessToken (?string $token)
    {
        if (empty($token)) return null;

        $sql = "SELECT * FROM {$this->_token_table} WHERE token_id = ? AND token = ?";
        $rs = self::$db->Execute($sql, array($this->user->asid, hash('sha256', $token)));

        $tokens = FieldsToObject($rs->fields);

        return $tokens;
    }

    /**
     * Given the ID, returns the given access token.
     *
     * @return AccessToken|null
     */
    public function getAccessTokenById(int $id)
    {
        $sql = "SELECT * FROM {$this->_token_table} WHERE token_id = ? AND uatid = ?";
        $rs = self::$db->Execute($sql, array($this->user->asid, $id));

        $tokens = FieldsToObject($rs->fields);

        return $tokens;
    }

    /**
     * Returns the current access token for the user.
     *
     * @return AccessToken
     */
    public function currentAccessToken()
    {
        return $this->attributes['activeAccessToken'] ?? null;
    }

    /**
     * Sets the current active token for this user.
     *
     * @return $this
     */
    public function withAccessToken($accessToken)
    {
        $this->attributes['activeAccessToken'] = $accessToken;

        return $this;
    }
}