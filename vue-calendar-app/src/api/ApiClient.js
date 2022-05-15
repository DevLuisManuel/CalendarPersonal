import request from "../utils/request.js";

export async function http(methods, baseURL, data) {
  const response = await request({
    url: baseURL,
    method: methods.toLowerCase(),
    data,
  });
  return response.data;
}
