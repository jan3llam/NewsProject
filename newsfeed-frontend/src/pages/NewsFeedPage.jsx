import React, { useEffect } from 'react';
import NewsFeed from '../components/NewsFeed/NewsFeed';
import Header from '../components/Header';
import useAuth from '../hooks/useAuth'; 
import { useNavigate } from 'react-router-dom';


const NewsFeedPage = () => {
  const navigate = useNavigate();
  const isAuthenticated = useAuth();

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
    }
  }, [isAuthenticated, navigate]);


  return (
    <div>
      <Header />
      <main>
        <NewsFeed />
      </main>
    </div>
  );
};

export default NewsFeedPage;
