from playwright.sync_api import sync_playwright, TimeoutError as PlaywrightTimeoutError

def get_rendered_html(url):
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        page.goto(url)

        try:
            page.wait_for_load_state('networkidle', timeout=60000)  # 增加超时时间
        except PlaywrightTimeoutError:
            print("Page loading timed out.")
        
        rendered_html = page.content()
        print(rendered_html)
        
        browser.close()

get_rendered_html('https://www.weather.com.cn/video/wjtq/index.shtml')
