const API_BASE_URL = import.meta.env.VITE_API_URL || '';
interface ApiResponse<T = any> {
  success?: boolean;
  data?: T;
  message?: string;
}

interface ApiError {
  message: string;
  status?: number;
}

export const api = {
  get: async <T = any>(
      endpoint: string,
      token?: string
  ): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      headers: token
          ? {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
          : {
            'Content-Type': 'application/json'
          }
    });
    if (!response.ok) {
      const error: ApiError = {
        message: `API Error: ${response.statusText}`,
        status: response.status
      };
      throw error;
    }
    return response.json();
  },

  post: async <T = any>(endpoint: string, data?: any): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: data ? JSON.stringify(data) : undefined
    });
    if (!response.ok) {
      const error: ApiError = {
        message: `API Error: ${response.statusText}`,
        status: response.status
      };
      throw error;
    }
    return response.json();
  },

  put: async <T = any>(endpoint: string, data?: any): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: data ? JSON.stringify(data) : undefined
    });
    if (!response.ok) {
      const error: ApiError = {
        message: `API Error: ${response.statusText}`,
        status: response.status
      };
      throw error;
    }
    return response.json();
  },

  patch: async <T = any>(endpoint: string, data?: any): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: data ? JSON.stringify(data) : undefined
    });
    if (!response.ok) {
      const error: ApiError = {
        message: `API Error: ${response.statusText}`,
        status: response.status
      };
      throw error;
    }
    return response.json();
  },

  delete: async <T = any>(endpoint: string): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      method: 'DELETE'
    });
    if (!response.ok) {
      const error: ApiError = {
        message: `API Error: ${response.statusText}`,
        status: response.status
      };
      throw error;
    }
    return response.json();
  }
};