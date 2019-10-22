/**
 * Created by ZEUS on 5/22/2018.
 */
import Home from './components/Pages/Home.vue';
import LeaderBoard from './components/Pages/leader_board.vue';

const routes = [
    // {
    //     path: '/home',
    //     component: Home,
    //     name: 'home'
    // },
    {
        path: '/chrecorder/public/',
        component: Home,
        name: 'home'
    },
    {
        path: '/chrecorder/public/leader-board',
        component: LeaderBoard,
        name: 'leader_board'
    },
];

export default routes;