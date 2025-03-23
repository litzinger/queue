import { MemoryRouter, Routes, Route } from 'react-router';
import './App.css'
import ViewFailedJob from './ViewFailedJob.tsx';
import ViewQueueJobs from './ViewQueueJobs.tsx';

function App() {
    return (
        <MemoryRouter
            initialEntries={['/cp/addons/settings/queue']}
            basename="/cp/addons/settings/queue"
        >
            <Routes>
                <Route path="/" element={<ViewQueueJobs />} />
                <Route path="/failed-job/:jobId" element={<ViewFailedJob />} />
            </Routes>
        </MemoryRouter>
    );
}

export default App;
