import React, { useState, useContext } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { logout as logoutService } from '../services/apiService';
import '../styles/HeaderStyles.css';
import { AuthContext } from '../context/AuthContext';
import FilterBar from './FilterBar';

const Header = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const navigate = useNavigate();
  const { logout } = useContext(AuthContext);
  const location = useLocation();

  const handleSearch = async (e) => {
    e.preventDefault();
    navigate(`/search/${encodeURIComponent(searchQuery)}`);
  };

  const handleLogout = async () => {
    try {
      const response = await logoutService();
      if (response.error) {
        console.error('Logout failed:', response.error);
      } else {
        logout();
      }
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  const navigateToSettings = () => {
    navigate('/settings');
  };

  const navigateToHome = () => {
    navigate('/news-feed');
  };

  return (
    <header className="header">
      <div className="header-container">
        {location.pathname !== '/news-feed' && (
          <button className="header-button" onClick={navigateToHome}>
            Home
          </button>
        )}
        {location.pathname !== '/settings' && (
          <button className="header-button" onClick={navigateToSettings}>
            Settings
          </button>
        )}
        <FilterBar />
        <form onSubmit={handleSearch} className="search-form">
          <input
            type="text"
            placeholder="Search..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="search-input"
          />
          <button type="submit" className="search-button">
            Search
          </button>
        </form>
        <button className="header-button" onClick={handleLogout}>
          Logout
        </button>
      </div>
    </header>
  );
};

export default Header;
