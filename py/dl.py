from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from bs4 import BeautifulSoup
import time
import os

# Set up Chrome options
chrome_options = Options()
chrome_options.add_argument("--headless")  # Run in headless mode

# Set up the WebDriver service
service = Service('/usr/local/bin/chromedriver')  # Update this path if necessary
driver = webdriver.Chrome(service=service, options=chrome_options)

try:
    # Open the login page
    driver.get("https://passport.weibo.com/sso/signin?entry=miniblog&source=miniblog&url=https%3A%2F%2Fs.weibo.com%2Fweibo%3Fq%3D%2523%25E9%25A2%2584%25E6%258A%25A5%25E5%25A4%25A9%25E5%25A4%25A9%25E7%259C%258B%2523")

    # Perform login
    username = driver.find_element(By.NAME, "username")
    password = driver.find_element(By.NAME, "password")
    login_button = driver.find_element(By.CSS_SELECTOR, 'button[type="submit"]')

    username.send_keys(os.environ['WEIBO_USERNAME'])
    password.send_keys(os.environ['WEIBO_PASSWORD'])
    login_button.click()

    # Wait for login to complete
    time.sleep(10)

    # Navigate to the target page
    driver.get("YOUR_TARGET_URL")  # Replace with your target URL

    # Extract page source
    page_source = driver.page_source
    with open("page_source.html", "w") as file:
        file.write(page_source)

    # Extract video URLs from page source
    soup = BeautifulSoup(page_source, "html.parser")
    
    # Find the <video-player> tag and extract the src attribute
    video_player_tag = soup.find("video-player")
    if video_player_tag:
        video_sources = video_player_tag.get("options")
        if video_sources:
            # Extract src from the options attribute
            start_idx = video_sources.find("src: '") + len("src: '")
            end_idx = video_sources.find("'", start_idx)
            video_url = video_sources[start_idx:end_idx]
            
            # Add the base URL if necessary
            if video_url.startswith('//'):
                video_url = 'https:' + video_url
            
            print("Extracted video URL:", video_url)

finally:
    driver.quit()
