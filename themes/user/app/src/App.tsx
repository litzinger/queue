import { useEffect, useState } from 'react'
import './App.css'
import config from './Config.ts'

function App() {
    const [queueStatus, setQueueStatus] = useState({
        size: 0
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch(config.urlQueueStatus);
                const result = await response.json();
                setQueueStatus(result);
            } catch (error) {
                console.error('Error fetching queue status:', error);
            }
        };

        const interval = setInterval(fetchData, 1000);

        fetchData();

        return () => clearInterval(interval);
    }, []);

  return (
      <div className="panel">
          <div className="panel-heading">
              <h2>Items in queue: {queueStatus.size}</h2>
          </div>
          <div className="panel-body">

          </div>
      </div>
  );
}

export default App;
