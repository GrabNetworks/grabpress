from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import NoSuchElementException
import unittest, time, re, sys

class GrabPressAutomation(unittest.TestCase):
    def setUp(self):
        self.driver = webdriver.Firefox()
        self.driver.implicitly_wait(10)
#        self.base_url = sys.argv[1]
	self.base_url = "http://grabpress-ci.grab-media.com/"
	self.verificationErrors = []
        self.accept_next_alert = True

    def tearDown(self):
        self.driver.quit()
        self.assertEqual([], self.verificationErrors)

    def Login(self):
	driver = self.driver
        driver.get(self.base_url + "wordpress/wp-login.php")
        driver.find_element_by_id("user_login").send_keys("\user")
        driver.find_element_by_id("user_pass").send_keys("bitnami")
        driver.find_element_by_id("wp-submit").click()
	self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"Welcome to WordPress!")

    def LoginAdminRole(self):
        driver = self.driver
        driver.get(self.base_url + "wordpress/wp-login.php")
        driver.find_element_by_id("user_login").send_keys("admin")
        driver.find_element_by_id("user_pass").send_keys("administrator")
        driver.find_element_by_id("wp-submit").click()
        self.assertRegexpMatches(driver.find_element_by_id("wp-admin-bar-my-account").text, r"Howdy, Admin Role")

    def LoginAuthorRole(self):
        driver = self.driver
        driver.get(self.base_url + "wordpress/wp-login.php")
        driver.find_element_by_id("user_login").send_keys("author")
        driver.find_element_by_id("user_pass").send_keys("author")
        driver.find_element_by_id("wp-submit").click()
        self.assertRegexpMatches(driver.find_element_by_id("wp-admin-bar-my-account").text, r"Howdy, Author Role")

    def LoginContributorRole(self):
        driver = self.driver
        driver.get(self.base_url + "wordpress/wp-login.php")
        driver.find_element_by_id("user_login").send_keys("contributor")
        driver.find_element_by_id("user_pass").send_keys("contributor")
        driver.find_element_by_id("wp-submit").click()
        self.assertRegexpMatches(driver.find_element_by_id("wp-admin-bar-my-account").text, r"Howdy, Contributor Role")

    def LoginEditorRole(self):
        driver = self.driver
        driver.get(self.base_url + "wordpress/wp-login.php")
        driver.find_element_by_id("user_login").send_keys("editor")
        driver.find_element_by_id("user_pass").send_keys("editor")
        driver.find_element_by_id("wp-submit").click()
        self.assertRegexpMatches(driver.find_element_by_id("wp-admin-bar-my-account").text, r"Howdy, Editor Role")

    def LoginSubscriberRole(self):
        driver = self.driver
        driver.get(self.base_url + "wordpress/wp-login.php")
        driver.find_element_by_id("user_login").send_keys("subscriber")
        driver.find_element_by_id("user_pass").send_keys("subscriber")
        driver.find_element_by_id("wp-submit").click()
        self.assertRegexpMatches(driver.find_element_by_id("wp-admin-bar-my-account").text, r"Howdy, Subscriber Role")

    def is_element_present(self, how, what):
        try: self.driver.find_element(by=how, value=what)
        except NoSuchElementException, e: return False
        return True

    def close_alert_and_get_its_text(self):
        try:
            alert = self.driver.switch_to_alert()
            if self.accept_next_alert:
                alert.accept()
            else:
                alert.dismiss()
            return alert.text
        finally: self.accept_next_alert = True


class CatalogTests(GrabPressAutomation):
    def test_CTLG_1_ExactPhraseSearch(self):
        driver = self.driver
        GrabPressAutomation.Login(self)
	driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-catalog")
        driver.find_element_by_id("keywords").clear()
        driver.find_element_by_id("keywords").send_keys("\"Spam Fries, Bacon Taco and Other Wacky Stadium Foods\"")
        driver.find_element_by_id("update-search").click()
        self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"Forget hot dogs, nachos, and normal ball park")
        self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"^[\s\S]*$")

    def test_CTLG_2_CreatePostFromCatalogSearch(self):
	driver = self.driver
        CatalogTests.test_CTLG_1_ExactPhraseSearch(self)
	driver.find_element_by_id("btn-create-feed-single-4400635").click()
	self.assertRegexpMatches(driver.find_element_by_id("content").text, r"grabpress_video guid=\"d811f4d7f6bd7de097b0e6dd09930411b44c0ab1\"")

    def test_CTLG_2a_DeleteCreatedPost(self):
	driver = self.driver
	GrabPressAutomation.Login(self)
	driver.get(self.base_url + "wordpress/wp-admin/edit.php")
	driver.find_element_by_id("post-search-input").send_keys("VIDEO: Spam Fries, Bacon Taco and Other Wacky Stadium Foods")
	driver.find_element_by_id("cb-select-all-1").click()
	Select(driver.find_element_by_name("action")).select_by_visible_text("Move to Trash")
        driver.find_element_by_id("doaction").click()
	driver.get(self.base_url + "wordpress/wp-admin/edit.php?post_status=trash&post_type=post")
	self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"VIDEO: Spam Fries, Bacon Taco and Other Wacky Stadium Foods")

class AccountTests(GrabPressAutomation):
    def UnlinkAccountNoLogin(self):
        driver = self.driver
        driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-account")
        driver.find_element_by_xpath("(//input[@name='action'])[2]").click()
        driver.find_element_by_id("confirm").click()
        driver.find_element_by_id("submit_button").click()
        self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"This installation is not linked to a Publisher account")    

    def test_ACCT_1_LinkExistingAccount(self):
        driver = self.driver
        GrabPressAutomation.Login(self)
        driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-account")
        driver.find_element_by_id("email").clear()
        driver.find_element_by_id("email").send_keys(r"jpduquette00@gmail.com")
        driver.find_element_by_id("password").clear()
        driver.find_element_by_id("password").send_keys(r"dukey177")
        driver.find_element_by_id("submit_button").click()
        # Warning: assertTextPresent may require manual changes
        self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"This installation is linked to jpduquette00@gmail.com")

    def test_ACCT_2_UnlinkAccount(self):
        driver = self.driver
        GrabPressAutomation.Login(self)
        driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-account")
        #driver.find_element_by_link_text("Account").click()
        driver.find_element_by_xpath("(//input[@name='action'])[2]").click()
        driver.find_element_by_id("confirm").click()
        driver.find_element_by_id("submit_button").click()
        self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"This installation is not linked to a Publisher account")

    def test_ACCT_3_CreateNewAccount(self):
        driver = self.driver
        GrabPressAutomation.Login(self)
        driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-account")
        driver.find_element_by_xpath("(//input[@name='action'])[2]").click()
        driver.find_element_by_id("id_email").clear()
        acct = "automated_user_test"
        acct += str(time.time())
        acct += "@grab-media.com"
        driver.find_element_by_id("id_email").send_keys(acct)
        driver.find_element_by_id("id_password").clear()
        driver.find_element_by_id("id_password").send_keys("test123")
        driver.find_element_by_id("id_password2").clear()
        driver.find_element_by_id("id_password2").send_keys("test123")
        driver.find_element_by_id("id_first_name").clear()
        driver.find_element_by_id("id_first_name").send_keys("Automated")
        driver.find_element_by_id("id_last_name").clear()
        driver.find_element_by_id("id_last_name").send_keys("User")
        driver.find_element_by_id("company").clear()
        driver.find_element_by_id("company").send_keys("Grab Media, Inc.")
        driver.find_element_by_id("id_address1").clear()
        driver.find_element_by_id("id_address1").send_keys("21000 Atlantic Blvd.")
        driver.find_element_by_id("id_address2").clear()
        driver.find_element_by_id("id_address2").send_keys("Suite 600")
        driver.find_element_by_id("id_city").clear()
        driver.find_element_by_id("id_city").send_keys("Sterling")
        Select(driver.find_element_by_id("id_state")).select_by_visible_text("Virginia")
        driver.find_element_by_id("id_zip").clear()
        driver.find_element_by_id("id_zip").send_keys("20166")
        driver.find_element_by_id("id_phone_number").clear()
        driver.find_element_by_id("id_phone_number").send_keys("703-555-5555")
        driver.find_element_by_id("id_site").clear()
        driver.find_element_by_id("id_site").send_keys("grab-media.com")
        driver.find_element_by_id("id_agree").click()
        driver.find_element_by_id("submit-button").click()
        self.assertRegexpMatches(driver.find_element_by_css_selector("BODY").text, r"This installation is linked to ")
        AccountTests.UnlinkAccountNoLogin(self)

    def test_ACNT_4_LinkNonExistingAccount(self):
	driver = self.driver
        GrabPressAutomation.Login(self)
        driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-account")
	driver.find_element_by_id("email").clear()
        driver.find_element_by_id("email").send_keys("no_way_this_user_exists@limbo.com")
        driver.find_element_by_id("password").clear()
        driver.find_element_by_id("password").send_keys("test")
        driver.find_element_by_id("submit_button").click()
        self.assertRegexpMatches(driver.find_element_by_id("message").text, r"No user with the supplied email and password combination exists in our system. Please try again.")

    def test_ACNT_5_CreateExistingAccount(self):
        driver = self.driver
        GrabPressAutomation.Login(self)
        driver.get(self.base_url + "wordpress/wp-admin/admin.php?page=gp-account")
        driver.find_element_by_xpath("(//input[@name='action'])[2]").click()
        driver.find_element_by_id("id_email").clear()
        driver.find_element_by_id("id_email").send_keys("jpduquette00@gmail.com")
        driver.find_element_by_id("id_password").clear()
        driver.find_element_by_id("id_password").send_keys("dukey177")
        driver.find_element_by_id("id_password2").clear()
        driver.find_element_by_id("id_password2").send_keys("dukey177")
        driver.find_element_by_id("id_first_name").clear()
        driver.find_element_by_id("id_first_name").send_keys("John")
        driver.find_element_by_id("id_last_name").clear()
        driver.find_element_by_id("id_last_name").send_keys("Duquette")
        driver.find_element_by_id("company").clear()
        driver.find_element_by_id("company").send_keys("Grab Media Inc.")
        driver.find_element_by_id("id_address1").clear()
        driver.find_element_by_id("id_address1").send_keys("21000 Atlantic Blvd.")
        driver.find_element_by_id("id_address2").clear()
        driver.find_element_by_id("id_address2").send_keys("Suite 600")
        driver.find_element_by_id("id_city").clear()
        driver.find_element_by_id("id_city").send_keys("Sterling")
        Select(driver.find_element_by_id("id_state")).select_by_visible_text("Virginia")
        driver.find_element_by_id("id_zip").clear()
        driver.find_element_by_id("id_zip").send_keys("20166")
        driver.find_element_by_id("id_phone_number").clear()
        driver.find_element_by_id("id_phone_number").send_keys("571-555-5555")
        driver.find_element_by_id("id_site").clear()
        driver.find_element_by_id("id_site").send_keys("grab-media.com")
        driver.find_element_by_id("id_agree").click()
        driver.find_element_by_id("submit-button").click()
        self.assertRegexpMatches(driver.find_element_by_id("message").text, r"We already have a registered user with the email address jpduquette00@gmail.com. If you would like to update your account information, please login to the")

#class InsertIntoPostTests(GrabPressAutomation):
#    def test_INPT_1_SearchCatalog(self):
#    def test_INPT_2_SortResults(self):

#class TemplateTests(GrabPressAutomation):
#    def test_TMPT_1_SetTemplateSize(self):
#    def test_TMPT_2_SetWideScreen(self):
#    def test_TMPT_3_SetStandardScreen(self):

#class DashboardTests(GrabPressAutomation):
#    def test_DASH_1_NoAccountLinked(self):
#    def test_DASH_2_LinkToCreateAccount(self):
#    def test_DASH_3_LinkToExistingAccount(self):

#class AutoposterTest(GrabPressAutomation):
#    def test_FEED_1_CreateFeed(self):
#    def test_FEED_2_PreviewFeed(self):
#    def test_FEED_3_PreviewFeedKeywords(self):
#    def test_FEED_4_ManageFeeds(self):
#    def test_FEED_5_DeleteFeed(self):
#    def test_FEED_6_EditFeed(self):
#    def test_FEED_7_NameFeed(self):
#    def test_FEED_8_WatchVideoFromPreview(self):
#    def test_FEED_9_FeedActiveToggle(self):

#class MiscTests(GrabPressAutomation):
#    def test_MISC_1_WordpressGrabpressVersion(self):
#    def test_MISC_2_MediaLibraryUploadImage(self):
#    def test_MISC_3_MediaLibraryUploadVideo(self):
#    def test_MISC_4_InsertImageIntoPost(self):
#    def test_MISC_5_InsertVideoIntoPost(self):
#    def test_MISC_6_AddCommentsToPost(self):

#class PermissionsTests(GrabPressAutomation):
#    def test_PERM_1_AdminPermissions(self):
#    def test_PERM_2_EditorPermissions(self):
#    def test_PERM_3_AuthorPermissions(self):
#    def test_PERM_4_ContributorPermissions(self):
#    def test_PERM_5_SubscriberPermissions(self):  



#searchTestSuite =  unittest.TestSuite()
#searchTestSuite.addTest(CatalogTests('test_CTLG_1_ExactPhraseSearch'))
#searchTestSuite.addTest(CatalogTests('test_CTLG_2_CreatePostFromCatalogSearch'))
#searchTestSuite.addTest(CatalogTests('test_CTLC_2a_DeleteCreatedPost'))
#suite = unittest.TestSuite(searchTestSuite)
#unittest.TextTestRunner(verbosity=2).run(suite)
if __name__ == "__main__":
    unittest.main()
