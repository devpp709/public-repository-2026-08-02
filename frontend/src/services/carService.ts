// src/services/carService.ts
const API_URL = process.env.NEXT_PUBLIC_API_URL || '';

export interface Car {
  id: number;
  brand: string;
  model: string;
  fullName: string;
  year: number;
  color: string;
  licensePlate: string;
  vin: string;
  mileage: number;
  fuelType: string;
  fuelTypeLabel: string;
  transmission: string;
  transmissionLabel: string;
  seats: number;
  doors: number;
  bags: number;
  dailyPrice: number;
  hourlyPrice: number;
  securityDeposit: number;
  isAvailable: boolean;
  status: string;
  statusLabel: string;
  description?: string;
  images?: CarImage[];
  mainImage?: string;
  carClass?: CarClass;
  location?: Location;
  features?: CarFeature[];
  extraServices?: ExtraService[];
  averageRating?: number;
  totalBookings?: number;
  totalRentalDays?: number;
  // Для обратной совместимости
  image?: string;
  img?: string;
  name?: string;
  price?: number;
  priceUnit?: string;
  rating?: number;
  totalTrips?: number;
  tag?: string[];
}

export interface CarImage {
  id: number;
  url: string;
  isMain: boolean;
  sortOrder: number;
}

export interface CarClass {
  id: number;
  name: string;
  description: string;
  icon: string;
  dailyRate: number;
  hourlyRate: number;
  createdAt: string;
  updatedAt: string;
  carsCount: number;
}

export interface Location {
  id: number;
  name: string;
  address: string;
  city: string;
  state: string;
  country: string;
  zipCode: string;
  phone: string;
  email: string;
  latitude: number;
  longitude: number;
  fullAddress: string;
  carsCount: number;
  availableCarsCount: number;
}

export interface CarFeature {
  id: number;
  feature: Feature;
  value: string;
}

export interface Feature {
  id: number;
  name: string;
  icon?: string;
}

export interface ExtraService {
  id: number;
  name: string;
  description: string;
  icon: string;
  category: string;
  categoryLabel: string;
  defaultPrice: number;
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
  carsCount: number;
  usageCount: number;
  priceForCar: number;
  isRequiredForCar: boolean;
}

export interface Review {
  id: number;
  rating: number;
  comment: string;
  createdAt: string;
}

export interface CarResponse {
  success: boolean;
  data: Car[];
  total?: number;
  page?: number;
  limit?: number;
}

export interface CarStatistics {
  total: number;
  available: number;
  rented: number;
  maintenance: number;
  reserved: number;
  avgDailyPrice: number;
  minDailyPrice: number;
  maxDailyPrice: number;
}

export interface SearchCriteria {
  brand?: string;
  model?: string;
  classId?: number;
  locationId?: number;
  minPrice?: number;
  maxPrice?: number;
  fuelType?: string;
  transmission?: string;
  seats?: number;
  available?: boolean;
  status?: string;
}

export interface RentResult {
  success: boolean;
  message: string;
  orderId?: string;
}

export interface UserData {
  name: string;
  email: string;
  phone?: string;
}

export const carService = {
  // Получить все машины
  getAllCars: async (withDetails: boolean = false): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars?withDetails=${withDetails}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching cars:', error);
      return [];
    }
  },

  rentCar: async (carId: string, userData: UserData): Promise<RentResult> => {
    const response = await fetch(`${API_URL}/v1/cars/${carId}/rent`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(userData),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  },

  // Получить машину по ID
  getCarById: async (id: number, withDetails: boolean = false): Promise<Car | null> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/${id}?withDetails=${withDetails}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: { success: boolean; data: Car } = await response.json();
      return data.data || null;
    } catch (error) {
      console.error(`Error fetching car ${id}:`, error);
      return null;
    }
  },

  // Получить популярные машины
  getPopularCars: async (limit: number = 10): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/popular?limit=${limit}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching popular cars:', error);
      return [];
    }
  },

  // Получить топ рейтинг
  getTopRatedCars: async (limit: number = 10): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/top-rated?limit=${limit}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching top rated cars:', error);
      return [];
    }
  },

  // Получить доступные машины с параметрами
  getAvailableCars: async (params?: {
    start_date?: string;
    end_date?: string;
    pickup_location?: string;
    dropoff_location?: string;
    return_same_location?: string;
    driver_age_18_40?: string;
    search?: string;
    class_id?: string;
    min_price?: string;
    max_price?: string;
  }): Promise<Car[]> => {
    try {
      const queryParams = new URLSearchParams();
      if (params) {
        Object.entries(params).forEach(([key, value]) => {
          if (value !== undefined && value !== null && value !== '') {
            queryParams.append(key, value);
          }
        });
      }

      const url = `${API_URL}/v1/cars/available${queryParams.toString() ? `?${queryParams.toString()}` : ''}`;

      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching available cars:', error);
      return [];
    }
  },

  // Получить машины по классу
  getCarsByClass: async (classId: number): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/class/${classId}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching cars by class:', error);
      return [];
    }
  },

  // Получить машины по локации
  getCarsByLocation: async (locationId: number): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/location/${locationId}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching cars by location:', error);
      return [];
    }
  },

  // Получить машины по цене
  getCarsByPriceRange: async (minPrice: number, maxPrice: number): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/price-range?min=${minPrice}&max=${maxPrice}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching cars by price range:', error);
      return [];
    }
  },

  // Поиск машин
  searchCars: async (criteria: SearchCriteria): Promise<Car[]> => {
    try {
      const params = new URLSearchParams();
      Object.entries(criteria).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          params.append(key, String(value));
        }
      });

      const response = await fetch(`${API_URL}/v1/cars/search?${params.toString()}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error searching cars:', error);
      return [];
    }
  },

  // Получить статистику
  getStatistics: async (): Promise<CarStatistics | null> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/statistics`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: { success: boolean; data: CarStatistics } = await response.json();
      return data.data || null;
    } catch (error) {
      console.error('Error fetching car statistics:', error);
      return null;
    }
  },

  // Получить статистику по брендам
  getBrandStatistics: async (): Promise<Array<{ brand: string; total: number }>> => {
    try {
      const response = await fetch(`${API_URL}v1/cars/statistics/brands`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: { success: boolean; data: Array<{ brand: string; total: number }> } = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching brand statistics:', error);
      return [];
    }
  },

  // Получить машины с изображениями
  getCarsWithImages: async (): Promise<Car[]> => {
    try {
      const response = await fetch(`${API_URL}/v1/cars/with-images`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data: CarResponse = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Error fetching cars with images:', error);
      return [];
    }
  }
};