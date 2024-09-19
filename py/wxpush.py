import os
import requests

# 设置代理服务器地址，从环境变量获取代理服务器IP和端口
proxies = {
    "http": os.getenv("PROXY_SERVER"),
    #"https": os.getenv("PROXY_SERVER")
}

# 从环境变量中获取 APP_ID 和 APP_SECRET
APP_ID = os.getenv("APP_ID")
APP_SECRET = os.getenv("APP_SECRET")

# 获取 access_token
def get_access_token(app_id, app_secret):
    url = f"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={app_id}&secret={app_secret}"
    response = requests.get(url, proxies=proxies)
    if response.status_code == 200:
        return response.json().get("access_token")
    else:
        raise Exception("获取 access_token 失败: " + response.text)

# 发送群发消息给指定标签用户
def send_message_to_tag(access_token, tag_id, message):
    url = f"https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={access_token}"
    data = {
        "filter": {
            "is_to_all": False,  # 是否发送给所有用户
            "tag_id": tag_id  # 指定标签ID
        },
        "text": {
            "content": message  # 消息内容
        },
        "msgtype": "text"  # 消息类型
    }
    response = requests.post(url, json=data, proxies=proxies)
    return response.json()

if __name__ == "__main__":
    try:
        # 获取 access_token
        access_token = get_access_token(APP_ID, APP_SECRET)

        # 发送消息给 'VIP_tag' 标签用户
        TAG_ID = os.getenv("TAG_ID")  # 从环境变量中获取 VIP_tag 的标签ID
        MESSAGE = "这是发送给 VIP 用户的消息"
        response = send_message_to_tag(access_token, TAG_ID, MESSAGE)

        print("群发消息结果：", response)

    except Exception as e:
        print(f"发生错误: {str(e)}")
