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

type Queue = {
    queueName: string;
    pendingCount: number
    pending: Array<Job>;
    failedCount: number;
    failed: Array<Job>;
};

type QueueStatusResponse = Array<Queue>;

function App() {
    const [queueStatus, setQueueStatus] = useState<QueueStatusResponse>([]);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch(config.urlQueueStatus, { cache: 'no-store' });
                const result: QueueStatusResponse = await response.json();

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
              <h2>Current Workload</h2>
          </div>
          <div className="panel-body panel-body__table">
              <div className="table-responsive table-responsive--collapsible">
                  <table>
                      <thead>
                      <tr className="app-listing__row app-listing__row--head">
                          <th>
                              Queue
                          </th>
                          <th>
                              Jobs
                          </th>
                      </tr>
                      </thead>
                      <tbody>
                          {queueStatus.map((queue: Queue) => {
                              return (
                                  <tr className="app-listing__row">
                                      <td>
                                          {queue.queueName}
                                      </td>
                                      <td>
                                          {queue.pendingCount || 0}
                                      </td>
                                  </tr>
                              )
                          })}
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  );
}

export default App;
