import { useState, useEffect } from 'react';
import { carService } from '../services/carService';

export const useCarRental = () => {
  const [cars, setCars] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchCars = async () => {
    setLoading(true);
    try {
      const data = await carService.getCars();
      setCars(data);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const rentCar = async (carId, userData) => {
    setLoading(true);
    try {
      const result = await carService.rentCar(carId, userData);
      await fetchCars(); // Обновляем список
      return result;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchCars();
  }, []);

  return { cars, loading, error, rentCar, fetchCars };
};
