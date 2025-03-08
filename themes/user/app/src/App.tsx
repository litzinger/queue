import { useEffect, useState } from 'react'
import './App.css'
import config from './Config.ts'

type Job = {
    id: string;
    queue: string;
    payload: string;
    attempts: number;
    reserved_at: number;
    available_at: number;
    created_at: number;
};

// type Jobs = Job[];

function App() {
    const [queueStatus, setQueueStatus] = useState({
        size: 0,
        pending: [],
        failed: []
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch(config.urlQueueStatus, { cache: 'no-store' });
                const result = await response.json();

                console.log(result);

                setQueueStatus({
                    'size': result.size,
                    'pending': result.pending,
                    'failed': result.failed,
                });
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
              <ul>
              {queueStatus.pending.map((job: Job) => {
                  const payload = JSON.parse(job.payload);

                  return (
                      <li>{job.id}: {payload.displayName}</li>
                  )
              })}
              </ul>
          </div>
      </div>
  );
}

export default App;
