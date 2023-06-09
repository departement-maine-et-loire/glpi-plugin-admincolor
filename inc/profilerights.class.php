<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}
class PluginAdmincolorProfileRights extends CommonDBTM
{

   static function hasChangeProfile()
   {
      if ((isset($_SESSION['glpi_plugin_admincolor_profile']['id']))) {
         if ($_SESSION['glpiactiveprofile']['id'] != $_SESSION['glpi_plugin_admincolor_profile']['id']) {
            $_SESSION['glpi_plugin_admincolor_profile']['id'] = $_SESSION['glpiactiveprofile']['id'];
            return true;
         } else {
            return false;
         }
      } else {
         $_SESSION['glpi_plugin_admincolor_profile']['id'] = $_SESSION['glpiactiveprofile']['id'];
      }
   }

   static function setSessionProfileId()
   {
      if (!isset($_SESSION['glpi_plugin_admincolor_profile'])) {
         $_SESSION['glpi_plugin_admincolor_profile']['id'] = $_SESSION['glpiactiveprofile']['id'];
      }
   }

   static function getSessionRights()
   {
      global $DB;

      $result = $DB->request([
         'SELECT' =>
         "right",
         'FROM' => 'glpi_plugin_admincolor_profile_rights',
         'WHERE' => [
            "profile"    => $_SESSION['glpiactiveprofile']['id']
         ],
      ]);
      foreach ($result as $data) {
         $right = $data['right'];
         return $right;
      }
   }

   static function getProfileRight($ID)
   {
      global $DB;

      $result = $DB->request([
         'SELECT' =>
         "right",
         'FROM' => 'glpi_plugin_admincolor_profile_rights',
         'WHERE' => [
            "profile"    => $ID
         ],
      ]);
      foreach ($result as $data) {
         $right = $data['right'];
         return $right;
      }
   }

   static function setSessionProfileRights()
   {
      $_SESSION['glpi_plugin_admincolor_profile']['right'] = self::getSessionRights();
   }

   static function changeProfile()
   {
      self::hasChangeProfile();
      self::setSessionProfileRights();
   }

   static function canUpdate()
   {

      if (isset($_SESSION["glpi_plugin_admincolor_profile"])) {
         return ($_SESSION['glpi_plugin_admincolor_profile']['right'] == 'w'
            || $_SESSION["glpi_plugin_admincolor_profile"]['right'] == 'su');
      }
      return false;
   }

   static function canView()
   {

      if (isset($_SESSION["glpi_plugin_admincolor_profile"])) {
         return ($_SESSION["glpi_plugin_admincolor_profile"]['right'] == 'w'
            || $_SESSION["glpi_plugin_admincolor_profile"]['right'] == 'r'
            || $_SESSION["glpi_plugin_admincolor_profile"]['right'] == 'su');
      }
      return false;
   }

   static function canProfileUpdate($ID)
   {
      $right = self::getProfileRight($ID);
      if (isset($right)) {
         return ($right == 'w'
            || $right == 'su');
      }
      return false;
   }

   static function canProfileView($ID)
   {
      $right = self::getProfileRight($ID);
      if (isset($right)) {
         return ($right == 'w'
            || $right == 'r'
            || $right == 'su');
      }
      return false;
   }

   static function isSuperAdmin()
   {
      if (isset($_SESSION["glpi_plugin_admincolor_profile"])) {
         return $_SESSION["glpi_plugin_admincolor_profile"]['right'] == 'su';
      }
      return false;
   }

   static function createAdminAccess($ID)
   {
      global $DB;

      $myProfile = new self();
      // if the profile does not already exist in the profile table of the plugin
      if (!$myProfile->getFromDB($ID)) {
         // Add a field in the table including the profile ID of the connected user and the right to write
         $DB->insert(
            'glpi_plugin_admincolor_profile_rights',
            [
               'profile' => $ID,
               'right' => 'su'
            ]
         );
      }
   }

   static function updateProfile($data)
   {
      global $DB;
      foreach ($data as $value) {
         $read = $value['readValue'];
         $update = $value['updateValue'];
         $id = $value['id'];
         $right = 'no';
         if ($read == 'true') {
            $right = 'r';
         }
         if ($update == 'true') {
            $right = 'w';
         }

         if ($id == 4) {
            return false;
         }

         $DB->updateOrInsert(
            'glpi_plugin_admincolor_profile_rights',
            [
               'profile'      => $id,
               'right'  => $right
            ],
            [
               'profile' => $id
            ]
         );

         return true;
      }
   }

   static function isAskerIdIdentic($id){
      if($_SESSION['glpiID'] == $id) {
         return true;
      }
      return false;
   }

   static function addErrorMessage($message) {
      Session::addMessageAfterRedirect(
         $message,
         false,
         ERROR
     );
     echo json_encode(['success' => false]);
     Html::displayAjaxMessageAfterRedirect();
   }

   static function addSuccessMessage($message) {
      Session::addMessageAfterRedirect(
         $message,
         false,
         INFO
     );
     echo json_encode(['success' => true]);
     Html::displayAjaxMessageAfterRedirect();
   }
}
