<?php


interface Core_Auth_Service_Interface extends Core_Service_Interface {

    /**
     * Login Method
     *
     * Fetches authentication information
     * Checks that those information are valid or not
     *
     * Returns TRUE on SUCCESS, FALSE otherwise
     *
     * @return boolean
     */
    public function login();

    /**
     * Logout Method
     *
     * Destroys the session
     */
    public function logout();

    /**
     * Checks the Validity Of the Current Session
     *
     * TRUE if the online user is authorized, FALSE otherwise
     *
     * @return boolean
     */
    public function isLoggedIn();

    /**
     * Check Authorization dedicated to Module Methods
     *
     * TRUE if the access is granted, FALSE otherwise
     *
     * @param string $module
     * @param string $method
     * @param int $uid
     * @return bool
     */
    public function checkMethodAuthorization($module = '', $method = '', $uid = 0);

    /**
     * Check Authorization dedicated to Module Pages
     *
     * TRUE if the access is granted, FALSE otherwise
     *
     * @param string $module
     * @param string $page
     * @param int $uid
     * @return bool
     */
    public function checkPageAuthorization($module = '', $page = '', $uid = 0);

    /**
     * Gets the User-Id of the current user
     *
     * @return int
     */
    public function getUid();
}