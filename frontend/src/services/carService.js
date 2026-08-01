const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001';

export const carService = {
  // Получить все машины
  getAllCars: async () => {
    try {
      const response = await fetch(`${API_URL}/api/v1/cars`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching cars:', error);
      return [];
    }
  },

  // Получить популярные машины
  getPopularCars: async (limit = 10) => {
    try {
      const response = await fetch(`${API_URL}/api/v1/cars/popular?limit=${limit}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching popular cars:', error);
      return [];
    }
  },

  // Получить доступные машины
  getAvailableCars: async (startDate, endDate) => {
    try {
      let url = `${API_URL}/api/v1/cars/available`;
      if (startDate && endDate) {
        url += `?start_date=${startDate}&end_date=${endDate}`;
      }
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching available cars:', error);
      return [];
    }
  }
};