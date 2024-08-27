from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager

# 设置 Chrome 浏览器选项
chrome_options = Options()
chrome_options.add_argument("--headless")  # 启动无头模式（可选）
chrome_options.add_argument("--disable-gpu")  # 仅在 Windows 上启用（可选）

# 初始化浏览器
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=chrome_options)

try:
    # 打开网页
    driver.get('https://m.weibo.cn/search?containerid=100103type%3D1%26q%3D%23%E9%A2%84%E6%8A%A5%E5%A4%A9%E5%A4%A9%E7%9C%8B%23')  # 替换为目标 URL

    # 等待直到页面标题包含特定文本
    WebDriverWait(driver, 20).until(
        EC.title_contains("预报天天看")  # 替换为实际页面标题的文本
    )

    # 或者等待页面中的特定元素加载
    WebDriverWait(driver, 20).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "div.thumbnail"))  # 替换为你需要等待的元素的选择器
    )

    # 输出网页内容
    print(driver.page_source)

finally:
    # 关闭浏览器
    driver.quit()
