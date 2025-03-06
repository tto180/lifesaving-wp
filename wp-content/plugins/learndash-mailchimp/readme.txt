=== LearnDash - Mailchimp ===
Contributors: d4mation, brashrebel, joelyoder
Requires at least: 4.5.0
Tested up to: 6.0.2
Stable tag: 1.5.0
Requires PHP: 5.4.0
Requires LearnDash: 2.2.1.2
LearnDash tested up to: 4.3.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let LearnDash students opt-in to MailChimp lists and tag them by course.

== Description ==

Let LearnDash students opt-in to MailChimp lists and tag them by course.

== Changelog ==

= 1.5.0 =
- Support's LearnDash's built-in Course Cloner to prevent the stored Mailchimp Tag for a Course getting copied to the new copy

= 1.4.0 =
- Moves the License Field to a sub-page under LearnDash LMS -> Settings -> Mailchimp -> Licensing as the LearnDash Hub Plugin (automatically installed when updating to LearnDash v4.3.0.2) removes the LMS License Tab from view

= 1.3.0 =
- Adds a message at the top of the Settings screen directing the user to enter their License key if not already done
- Adds a description to the API Key field to tell the userr where to get one
- Adds a link to the plugins listing directing the user to enter their License Key if not already done
- Fixes a visual bug with the "Tag All Students" button
- Updates our Licensing and Support module to the latest version

= 1.2.0 =
- Prevent PHP notice showing for not logged-in users
- Update Licensing and Support module to the latest version
  - Now the Changelog will show directly within your website

= 1.1.4 =
- Prevents LearnDash Content Cloner from copying over saved Mailchimp Tags
  - These will get set for the cloned Course once it is published

= 1.1.3 =
- Includes some debug info for each API transaction in the Error Log
- If a Student already exists in Mailchimp, update any relevant information such as their Name
- Fixes an issue with the instantiation of Admin Notices on plugin startup

= 1.1.2 =
- Fixes an issue where Course Tags would no longer be created automatically on Course Creation once Auto-Subscribe was enabled.
- Adds Tags for Groups if Auto-Subscribe is enabled. Students will be automatically added to a Tag for the Group as well as each Course within the Group on being added to a Group.
- Adds the learndash_mailchimp_auto_subscribe_user Filter for bailing on Auto-Subscription.

= 1.1.1 =
- Prevents Manual Subscription form from showing if Auto-Subscribe is enabled.

= 1.1.0 =
- Auto-Subscription on Course Enrollment is here!
  - This will not trigger for Open Courses due to how LearnDash handles Course Enrollment. Your Course Access Mode will need to be set to something else.
- If Auto-Subscription is enabled, a new button will appear on the settings page to automatically Tag all of your existing Students with the appropriate Tags for each Course they are enrolled in and/or have begun.

= 1.0.5 =
- Fixes an issue where if a Tag with a matching name already existed in Mailchimp the plugin would not save the corresponding Tag ID to the Course
- Fixes an issue where if you had more than 10 Lists they would not all show on the Settings Page
- Changes references to “Segments” to “Tags” to match up with how Mailchimp now calls them
- Changes all references to “MailChimp” to “Mailchimp” to match their new branding
- Allows updating the Success Message via the Settings Page
Updates our Licensing and Support module to the latest version

= 1.0.4 =
- Revert change from v1.0.3
- Added button to clear all created List Segments by the Plugin in MailChimp. This allows you “uninstall”/”reinstall” the plugin by starting fresh.
  - Keep in mind, this will remove any Subscribers from these Segments!
- Added a checkbox below the License Key field at LearnDash LMS -> Settings -> LMS License to opt-in to Beta Releases.

= 1.0.3 =
- Fixes a bug caused by a change to the MainChimp API

= 1.0.2 =
- Fixes typo in Plugin Header
- Updates our Licensing and Support module to the latest version

= 1.0.1 =
- Fixes PHP error if ld_mailchimp_get_list_segment_emails() fails
- Updates our Licensing and Support module to the latest version

= 1.0.0 =
- Initial release
