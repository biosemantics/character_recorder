/**
 * Created by ZEUS on 5/22/2018.
 */
import Home from './components/Pages/Home.vue';
import LeaderBoard from './components/Pages/leader_board.vue';
import ExploreCharacter from './components/Pages/explore_character.vue';
import OntologyUpdate from './components/Pages/ontology_update.vue';
import SharedCharacter from './components/Pages/shared_character.vue';

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
    {
        path: '/chrecorder/public/explore-character',
        component: ExploreCharacter,
        name: 'explore_character'
    },
    {
        path: '/chrecorder/public/ontology-update',
        component: OntologyUpdate,
        name: 'ontology_update'
    },
    {
        path: '/chrecorder/public/shared-character',
        component: SharedCharacter,
        name: 'shared_character'
    },
];

export default routes;