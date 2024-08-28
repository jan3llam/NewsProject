import React from 'react';
import PropTypes from 'prop-types';
import '../../styles/NewsFeedStyles.css'; 

const ArticleCard = ({ title, description, url, source, author, image, publishedAt }) => (
  <div className="article-card">
    {image && <img src={image} alt={title} className="article-card__image" />}
    <div className="article-card__content">
      <h2 className="article-card__title">{title}</h2>
      <p className="article-card__description">{description}</p>
      <p className="article-card__meta">
        <span>{source}</span> | <span>{author}</span> | <span>{publishedAt}</span>
      </p>
      <a href={url} className="article-card__link" target="_blank" rel="noopener noreferrer">
        Read More
      </a>
    </div>
  </div>
);

ArticleCard.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.string,
  url: PropTypes.string.isRequired,
  source: PropTypes.string.isRequired,
  author: PropTypes.string,
  image: PropTypes.string,
  publishedAt: PropTypes.string.isRequired,
};

export default ArticleCard;
