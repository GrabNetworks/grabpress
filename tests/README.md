GrabPress Automation Testing

Usage:
python <filename> <base_url>
i.e: python firefox.py 'http://grabpress-ci.grab-media.com/'

Creating New Test Cases:
1. Using selenium IDE, record the steps you want to tests.
2. Export the test case as Python/ unittest / WebDriver
3. Make any necessary changes to the exported file.
4. Run locally to make sure it works out.
5. Add the test case to browser type file (Currently only running against Firefox).

References:
Python Unittest: http://docs.python.org/2/library/unittest.html
Python WebElements: http://selenium-python.readthedocs.org/en/latest/api.html#module-selenium.webdriver.remote.webelement
