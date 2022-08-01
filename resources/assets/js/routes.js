/**
 * Created by ZEUS on 5/22/2018.
 */
import Home from './components/Pages/Home.vue';
import LeaderBoard from './components/Pages/leader_board.vue';
import ExploreCharacter from './components/Pages/explore_character.vue';
import OntologyUpdate from './components/Pages/ontology_update.vue';
import SharedCharacter from './components/Pages/shared_character.vue';

const routes = [
    {
        path: '/home',
        redirect: '/'
    },
    {
        path: '/',
        component: Home,
        name: 'home'
    },
    {
        path: '/leader-board',
        component: LeaderBoard,
        name: 'leader_board'
    },
    {
        path: '/explore-character',
        component: ExploreCharacter,
        name: 'explore_character'
    },
    {
        path: '/ontology-update',
        component: OntologyUpdate,
        name: 'ontology_update'
    },
    {
        path: '/shared-character',
        component: SharedCharacter,
        name: 'shared_character'
    },
];

export default routes;
