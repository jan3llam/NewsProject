// src/pages/SearchResultsPage.js
import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import useFetchNews from '../hooks/useFetchNews';
import SearchResults from '../components/NewsFeed/SearchResults';
import Header from '../components/Header';
import useAuth from '../hooks/useAuth';
import { useNavigate } from 'react-router-dom';

const SearchResultsPage = () => {
  const { query } = useParams();
  const [currentPage, setCurrentPage] = useState(1);
  const { articles, loading, error } = useFetchNews(query, currentPage);

  const navigate = useNavigate();
  const isAuthenticated = useAuth();

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
    }
  }, [isAuthenticated, navigate]);


  const handlePageChange = (newPage) => {
    setCurrentPage(newPage);
  };


  return (
    <div>
      <Header />
      <main>
        <SearchResults
          data={articles.data}
          loading={loading}
          error={error}
          query={query}
          currentPage={currentPage}
          onPageChange={handlePageChange}
        />
      </main>
    </div>
  );
};

export default SearchResultsPage;
