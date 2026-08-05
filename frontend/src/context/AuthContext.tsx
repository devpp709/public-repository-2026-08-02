import React, {
  createContext,
  useContext,
  useEffect,
  useState,
  ReactNode
} from 'react';

import { api } from '../services/api';


interface User {
  id: string;
  email: string;
  name?: string;
  role?: string;
}


interface AuthResponse {
  token: string;
  user: User;
}


export interface AuthContextType {
  user: User | null;
  accessToken: string | null;
  loading: boolean;
  login(email: string, password: string): Promise<AuthResponse>;
  logout(): void;
  refreshAccessToken(): Promise<string | null>;
}


interface Props {
  children: ReactNode;
}


export const AuthContext = createContext<AuthContextType | null>(null);


export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error('useAuth must be used inside AuthProvider');
  }

  return context;
};


export const AuthProvider = ({children}: Props) => {

  const [user, setUser] = useState<User | null>(null);
  const [accessToken, setAccessToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);


  useEffect(() => {

    const token = localStorage.getItem('token');

    if (!token) {
      setLoading(false);
      return;
    }

    setAccessToken(token);

    api.get<User>('/auth/me', token)
        .then(setUser)
        .catch(() => logout())
        .finally(() => setLoading(false));

  }, []);



  const login = async (
      email:string,
      password:string
  ):Promise<AuthResponse> => {

    const response = await api.post<AuthResponse>(
        '/auth/login',
        {
          email,
          password
        }
    );


    localStorage.setItem(
        'token',
        response.token
    );

    setAccessToken(response.token);
    setUser(response.user);


    return response;
  };



  const logout = () => {

    localStorage.removeItem('token');

    setAccessToken(null);
    setUser(null);

  };



  const refreshAccessToken = async ():Promise<string|null> => {

    const token = localStorage.getItem('token');

    if (!token) {
      return null;
    }

    setAccessToken(token);

    return token;
  };



  return (
      <AuthContext.Provider
          value={{
            user,
            accessToken,
            loading,
            login,
            logout,
            refreshAccessToken
          }}
      >
        {children}
      </AuthContext.Provider>
  );
};