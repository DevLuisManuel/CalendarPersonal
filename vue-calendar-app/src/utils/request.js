/* eslint-disable no-console */
import axios from "axios";

const service = axios.create({
  baseURL: `${process.env.VUE_APP_BASE_URL}`,
});

service.interceptors.request.use(
  (config) => {
    config.headers["Accept"] = "application/json";
    config.headers["Content-Type"] = "application/json";
    return config;
  },
  (e) => {
    return Promise.reject(e);
  }
);

service.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default service;
