import React from 'react';
import PropTypes from 'prop-types';
import '../../styles/PreferencesSectionStyles.css'; 

const PreferenceSection = ({ title, options, selectedOptions, onChange, pagination, loading=false }) => (
  <div className="preference-section">
    <h3>{title}</h3>
    {loading ? (
      <div className="loading-message">Loading {title.toLowerCase()}...</div>
    ) : (
      <>
        <div className="preference-grid">
          {options.map((option) => (
            <label key={option} className="preference-card">
              <input
                type="checkbox"
                checked={selectedOptions.includes(option)}
                onChange={() => onChange(option)}
              />
              {option}
            </label>
          ))}
        </div>
        <div className="pagination-controls">
          <button
            onClick={pagination.onPrev}
            disabled={pagination.currentPage === 1}
          >
            Previous
          </button>
          <span>{pagination.currentPage} / {pagination.totalPages}</span>
          <button
            onClick={pagination.onNext}
            disabled={pagination.currentPage === pagination.totalPages}
          >
            Next
          </button>
        </div>
      </>
    )}
  </div>
);

PreferenceSection.propTypes = {
  title: PropTypes.string.isRequired,
  options: PropTypes.arrayOf(PropTypes.string).isRequired,
  selectedOptions: PropTypes.arrayOf(PropTypes.string).isRequired,
  onChange: PropTypes.func.isRequired,
  pagination: PropTypes.shape({
    currentPage: PropTypes.number.isRequired,
    totalPages: PropTypes.number.isRequired,
    onNext: PropTypes.func.isRequired,
    onPrev: PropTypes.func.isRequired,
  }).isRequired,
  loading: PropTypes.bool, 
};


export default PreferenceSection;
