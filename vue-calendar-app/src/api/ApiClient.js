import axios from "axios";

const config = {
  baseURL: process.env.API_URL,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
};

export default axios.create(config);
