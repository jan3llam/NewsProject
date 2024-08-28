import { useContext } from 'react';
import { AuthContext } from '../context/AuthContext';

const useAuth = () => {
  const { authToken } = useContext(AuthContext);
  return Boolean(authToken); 
};

export default useAuth;
