from playwright.sync_api import sync_playwright

def get_rendered_html(url):
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        page.goto(url)
        
        # 等待页面完全加载
        page.wait_for_load_state('networkidle')
        
        # 获取渲染后的HTML
        rendered_html = page.content()
        print(rendered_html)
        
        browser.close()

get_rendered_html('https://www.weather.com.cn/video/wjtq/index.shtml')
