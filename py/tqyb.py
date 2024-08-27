from selenium import webdriver

# 使用 Chrome 浏览器
driver = webdriver.Chrome()

# 打开目标网址
driver.get('https://m.weibo.cn/search?containerid=100103type%3D1%26q%3D%23%E9%A2%84%E6%8A%A5%E5%A4%A9%E5%A4%A9%E7%9C%8B%23')  # 替换为目标 URL

# 等待动态内容加载
driver.implicitly_wait(10)  # 等待 10 秒

# 获取完整的 HTML 内容
html = driver.page_source
print(html)

# 关闭浏览器
driver.quit()
