import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { getUserPreferences } from '../services/apiService'; 
import '../styles/FilterBarStyles.css';
import queryString from 'query-string';

const FilterBar = () => {
  const [selectedFilter, setSelectedFilter] = useState('');
  const [filterValue, setFilterValue] = useState('');
  const navigate = useNavigate();
  const [selectedPreferences, setSelectedPreferences] = useState({ categories: [], sources: [] });
  const [error, setError] = useState('');

  const handleFilterSubmit = async (event) => {
    event.preventDefault();
    const filters = {};

    if (selectedFilter === 'category' || selectedFilter === 'source') {
      if (filterValue) {
        filters[selectedFilter] = filterValue;
      }
    } else if (selectedFilter === 'date') {
      if (filterValue) {
        filters.date = filterValue;
      }
    }
    
    const queryStringFilters = queryString.stringify(filters);

    navigate(`/filter?${queryStringFilters}`);
  };

  useEffect(() => {
    const fetchPreferences = async () => {
      try {
        const userPrefsData = await getUserPreferences();
        setSelectedPreferences({
          categories: userPrefsData.data.preferred_categories || ['No preferred categories'],
          sources: userPrefsData.data.preferred_sources || ['No preferred sources'],
        });
      } catch (err) {
        setError('Failed to load data');
        console.error(err);
      }
    };

    fetchPreferences();
  }, []); 

  return (
    <form onSubmit={handleFilterSubmit} className="filter-bar">
      <select
        value={selectedFilter}
        onChange={(e) => {
          setSelectedFilter(e.target.value);
          setFilterValue(''); 
        }}
      >
        <option value="">Select Filter</option>
        <option value="source">Source</option>
        <option value="category">Category</option>
        <option value="date">Date</option>
      </select>

      {selectedFilter === 'category' && (
        <select
          value={filterValue}
          onChange={(e) => setFilterValue(e.target.value)}
        >
          <option value="">Select Category</option>
          {selectedPreferences.categories.map((category) => (
            <option key={category} value={category}>
              {category}
            </option>
          ))}
        </select>
      )}

      {selectedFilter === 'source' && (
        <select
          value={filterValue}
          onChange={(e) => setFilterValue(e.target.value)}
        >
          <option value="">Select Source</option>
          {selectedPreferences.sources.map((source) => (
            <option key={source} value={source}>
              {source}
            </option>
          ))}
        </select>
      )}

      {selectedFilter === 'date' && (
        <input
          type="date"
          value={filterValue}
          onChange={(e) => setFilterValue(e.target.value)}
        />
      )}

      <button type="submit" disabled={!filterValue}>
        Apply Filter
      </button>
      {error && <div className="error-message">{error}</div>}
    </form>
  );
};

export default FilterBar;
