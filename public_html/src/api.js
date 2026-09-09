import axios from 'axios';

export const API_BASE_URL = (() => {
  const envUrl = import.meta.env.VITE_API_BASE_URL;
  if (envUrl) {
    return envUrl.replace(/\/+$/, '');
  }
  if (typeof window !== 'undefined') {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      return 'http://localhost:8080/api';
    }
    return `${window.location.origin}/api`;
  }
  return 'http://localhost:8080/api';
})();

export const getBaseUrl = () => {
  return API_BASE_URL.replace(/\/api\/?$/, '');
};

export const getFileUrl = (folder, filename) => {
  if (!filename) return '';
  const base = getBaseUrl();
  const formattedBase = base.endsWith('/') ? base.slice(0, -1) : base;
  return `${formattedBase}/api/files/${folder}/${filename}`;
};

const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 60000,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('authToken');
    const userStr = localStorage.getItem('user');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    if (userStr) {
      try {
        const user = JSON.parse(userStr);
        if (user && user.id) {
          config.headers['X-User-Id'] = user.id;
        }
      } catch(e) {}
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('API Error:', error);
    console.error('Error config:', error.config);
    console.error('Error response:', error.response);
    console.error('Error request:', error.request);

    if (error.response) {
      // Server responded with error status
      console.error('Server error:', error.response.status, error.response.data);
      return Promise.reject(error.response.data);
    } else if (error.request) {
      // Request made but no response
      console.error('No response received:', error.request);
      return Promise.reject({
        message: 'Please Refresh the page and try again',
        details: error.message,
        url: error.config?.url
      });
    } else {
      // Error in request setup
      console.error('Request setup error:', error.message);
      return Promise.reject({
        message: 'Error preparing request',
        details: error.message
      });
    }
  }
);

export default api;
