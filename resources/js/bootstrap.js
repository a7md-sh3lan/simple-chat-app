import store from './state/store'
import axios from 'axios';
import axiosRetry from 'axios-retry';
import appConfig from "@/app.config";


let originUrl = window.location.origin;
axios.defaults.baseURL = originUrl + '/api/';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Authorization'] = store.state.auth.user.token != undefined ? store.state.auth.user.token : '';
axios.defaults.withCredentials = true;

axiosRetry(axios, { retries: 3 });
window.axios = require('axios');

function setAxiosInterceptors(){
    window.axios.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        if (error.response.status == 401) {// Unauthenticated
            // window.axios.get('refresh',{
            //     'axios-retry': {
            //         retries: 1
            //     }
            // }).then(function(res){
            //     store.dispatch('auth/refresh', res)
            // }).catch(function(error){
                store.dispatch('auth/logout')
                window.location.href = '/login';
            // });
        }
        if (error.response.status == 403) {
        //   that.$iziToast.error({message: 'Permission Denied', position: 'bottomCenter', timeout: 5000});
            alert('Permission Denied');
        }
        return Promise.reject(error);
    });
}

setAxiosInterceptors();

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
window.io = require('socket.io-client')

let echoConfig = {
    broadcaster: 'socket.io',
    host: window.location.hostname,
    transports: ['websocket', 'polling', 'flashsocket'], // Fix CORS error!
    debug: true
};

if (appConfig.socket.port != null)
{
    echoConfig['host'] = echoConfig.host +  ':' + appConfig.socket.port;
} else {
    echoConfig['path'] = appConfig.socket.path;
}

console.log(echoConfig);


window.Echo = new Echo(echoConfig);

