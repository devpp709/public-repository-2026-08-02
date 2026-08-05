import { useState, useEffect, useCallback } from 'react';
import { carService, Car, CarResponse } from '../services/carService';

interface UserData {
  name: string;
  email: string;
  phone?: string;
}

interface RentResult {
  success: boolean;
  message: string;
  orderId?: string;
}

export const useCarRental = () => {
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const fetchCars = useCallback(async (): Promise<void> => {
    setLoading(true);
    try {
      const data = await carService.getAllCars();
      setCars(data);
      setError(null);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error');
    } finally {
      setLoading(false);
    }
  }, []);

  const rentCar = useCallback(async (carId: string, userData: UserData): Promise<RentResult> => {
    setLoading(true);
    try {
      const result = await carService.rentCar(carId, userData);
      await fetchCars();
      return result;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Unknown error';
      setError(errorMessage);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [fetchCars]);

  useEffect(() => {
    fetchCars();
  }, [fetchCars]);

  return {
    cars,
    loading,
    error,
    rentCar,
    fetchCars
  };
};