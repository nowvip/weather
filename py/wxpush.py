import os
import requests
import logging

# 配置日志输出
logging.basicConfig(level=logging.INFO)

# 从环境变量中获取 APP_ID 和 APP_SECRET
APP_ID = os.getenv("APP_ID")
APP_SECRET = os.getenv("APP_SECRET")

# 设置代理服务器地址
proxies = {
    "https": os.getenv("PROXY_SERVER")
}

# 获取 access_token
def get_access_token(app_id, app_secret):
    url = "https://api.weixin.qq.com/cgi-bin/token"
    params = {
        "grant_type": "client_credential",
        "appid": app_id,
        "secret": app_secret
    }
    try:
        response = requests.get(url, params=params, proxies=proxies)
        if response.status_code == 200:
            result = response.json()
            if result.get("errcode") == 0:  # 确保没有返回错误
                logging.info("成功获取 access_token")
                return result.get("access_token")
            else:
                raise Exception(f"获取 access_token 失败: {result['errmsg']}")
        else:
            raise Exception(f"请求失败，状态码: {response.status_code}, 原因: {response.text}")
    except Exception as e:
        raise Exception(f"获取 access_token 出现错误: {str(e)}")

# 发送群发消息给指定标签用户
def send_message_to_group(access_token, group_id, message):
    url = f"https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={access_token}"
    data = {
        "filter": {
            "is_to_all": False,  # 不发送给所有用户
            "group_id": group_id  # 指定的标签ID
        },
        "text": {
            "content": message  # 消息内容
        },
        "msgtype": "text"  # 消息类型
    }
    try:
        response = requests.post(url, json=data, proxies=proxies)
        result = response.json()
        if response.status_code == 200 and result.get("errcode") == 0:
            logging.info("消息发送成功")
        else:
            logging.error(f"群发消息失败: {result.get('errmsg', '未知错误')} (errcode: {result.get('errcode')})")
        return result
    except Exception as e:
        logging.error(f"发送消息请求出错: {str(e)}")
        return None

if __name__ == "__main__":
    try:
        # 获取 access_token
        access_token = get_access_token(APP_ID, APP_SECRET)

        # 指定标签的 group_id (假设是100)
        GROUP_ID = os.getenv("TAG_ID")  # 替换为实际的标签ID
        MESSAGE = "这是发送给 '天气预报' 标签用户的消息"
        
        # 发送消息
        response = send_message_to_group(access_token, GROUP_ID, MESSAGE)
        if response:
            print("群发消息结果：", response)
        else:
            logging.error("消息发送失败，没有返回有效的响应")
    except Exception as e:
        logging.error(f"程序运行过程中发生错误: {str(e)}")
