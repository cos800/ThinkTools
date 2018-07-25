
let app = getApp();

export default {
    promisify(api) {
        return (options, ...params) => {
            return new Promise((resolve, reject) => {
                api(Object.assign({}, options, { success: resolve, fail: reject }), ...params);
            }).catch(err=>{
                if (err.errMsg.indexOf('cancel') < 0) {
                    wx.showModal({
                        content: err.errMsg,
                        showCancel: false
                    });
                }
                // console.log(err);

                return Promise.reject(err);
            });
        }
    },

    apiSsid() {
        let user = wx.getStorageSync('user') || {};
        return user.SSID || '';
    },
    apiUrl(url) {
        let ssid = this.apiSsid();
        url = app.baseUrl + url + (url.indexOf('?')>=0 ? '&' : '?') + 'SSID=' + ssid;
        return url;
    },
    apiReq(url='', data={}, isPost=false) {
        return new Promise((resolve, reject)=>{

            wx.request({
                url: this.apiUrl(url),
                data: data,
                header: {
                    'content-type': 'application/json' // 默认值
                },
                method: isPost ? 'POST' : 'GET',
                dataType: 'json',
                success(res) {
                    let ret = res.data;

                    if (typeof ret !== 'object') {
                        reject({title:'响应异常',content:ret});
                    } else if (ret.ok===1) {
                        ret.msg && wx.showToast({
                            title: ret.msg,
                            icon: 'success',
                        });
                        resolve(ret);
                    } else if (ret.ok===0 && ret.msg) {
                        if (ret.msg==='请先登录') { // ssid失效之类错误
                            wx.removeStorageSync('user');
                            ret.msg = '登录状态失效，请重试。'
                        }

                        reject({title:'信息提示',content:ret.msg});
                    } else {
                        reject({title:'响应异常',content:JSON.stringify(ret)});
                    }
                },
                fail(err) {
                    reject({title:'网络错误',content:err.errMsg});
                },
            });

        }).catch(modal=>{
            modal.showCancel = false;
            wx.showModal(modal);
            // throw 'end';
            return Promise.reject(modal);
        });


    },

    api(...theArgs) {
        // 1. 有没有SSID
        // 2. 没有则登录获取SSID
        // 3. 调用apiReq

        let ssid = this.apiSsid();
        if (!ssid) {
            let wxLogin = this.promisify(wx.login);

            return wxLogin().then(res=>{
                return this.apiReq('wechat/mplogin', {code: res.code});
            }).then(res=>{
                return wx.setStorageSync('user', res.data.user);

            }).then((res)=>{
                return this.apiReq(...theArgs);
            });
        }else{
            return this.apiReq(...theArgs);
        }

    },

    log: console.log.bind(console),

    formatNumber(n) {
        n = n.toString()
        return n[1] ? n : '0' + n
    },

    formatDate(date=false) {
        if (!date) {
            date = new Date();
        }
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();

        return [year, month, day].map(this.formatNumber).join('-');
    },
}