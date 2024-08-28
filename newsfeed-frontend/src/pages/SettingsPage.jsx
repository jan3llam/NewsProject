import React from 'react';
import Settings from '../components/NewsFeed/Settings';
import Header from '../components/Header'; 
import useAuth from '../hooks/useAuth';
import { useNavigate } from 'react-router-dom';

const SettingsPage = () => {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();

    return (
        <div>
            <Header />
            <main>
                <Settings isAuthenticated={isAuthenticated} navigate={navigate} />
            </main>
        </div>
    );
};

export default SettingsPage;
