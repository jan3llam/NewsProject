import React, { useState, useContext, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { login as loginService } from '../services/apiService';
import { AuthContext } from '../context/AuthContext';
import useAuth from '../hooks/useAuth'; 
import '../styles/AuthStyles.css';

const LoginPage = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const { login } = useContext(AuthContext);
  const navigate = useNavigate();
  const isAuthenticated = useAuth();

  useEffect(() => {
    if (isAuthenticated) {
      navigate('/news-feed');
    }
  }, [isAuthenticated, navigate]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await loginService(email, password);
      if (response.error) {
        setError(response.error);
      }
      else {
        const token = response.data.token;
        const user = response.data.user;
        login(token, user);
      }

    } catch (error) {
      setError('Login failed. Please try again.');
    }
  };

  const navigateToRegister = () => {
    navigate('/register');
  };

  return (
    <div className="auth-container">
      <h2>Login</h2>
      {error && <p className="error-message">{error}</p>}
      <form onSubmit={handleSubmit} className="auth-form">
        <div className="form-group">
          <label htmlFor="email">Email:</label>
          <input
            type="email"
            id="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>
        <div className="form-group">
          <label htmlFor="password">Password:</label>
          <input
            type="password"
            id="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        <button type="submit">Login</button>
      </form>
      <div className="auth-footer">
        <p>Don't have an account?</p>
        <button type="button" onClick={navigateToRegister}>
          Register here
        </button>
      </div>
    </div>
  );
};

export default LoginPage;
