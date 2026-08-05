export const validators = {
  email: (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email) ? null : 'Некорректный email';
  },

  phone: (phone) => {
    const re = /^\+?[0-9]{10,15}$/;
    return re.test(phone.replace(/\s/g, '')) ? null : 'Некорректный номер телефона';
  },

  required: (value, fieldName = 'Поле') => {
    return value && value.trim() ? null : \ обязательно для заполнения;
  },

  minLength: (min) => (value) => {
    return value && value.length >= min ? null : Минимальная длина: \ символов;
  },

  maxLength: (max) => (value) => {
    return value && value.length <= max ? null : Максимальная длина: \ символов;
  }
};
