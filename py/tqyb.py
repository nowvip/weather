# your_test_script.py
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager

# 设置 Chrome 浏览器选项（可选）
chrome_options = Options()
chrome_options.add_argument("--headless")  # 启动无头模式（可选）
chrome_options.add_argument("--disable-gpu")  # 仅在 Windows 上启用（可选）

# 初始化浏览器
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=chrome_options)

# 打开网页
driver.get('https://m.weibo.cn/search?containerid=100103type%3D1%26q%3D%23%E9%A2%84%E6%8A%A5%E5%A4%A9%E5%A4%A9%E7%9C%8B%23')  # 替换为目标 URL

# 输出网页内容
print(driver.page_source)

# 关闭浏览器
driver.quit()
