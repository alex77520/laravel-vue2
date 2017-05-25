// http://router.vuejs.org/zh-cn/advanced/lazy-loading.html
const Index = r => require.ensure([], () => r(require('../views/index.vue')), 'index');
const LogApp = r => require.ensure([], () => r(require('../views/logApp.vue')), 'logApp');
const LogType = r => require.ensure([], () => r(require('../views/logType.vue')), 'logType');
const LogTable = r => require.ensure([], () => r(require('../views/logTable.vue')), 'logTable');
const Welcome = r => require.ensure([], () => r(require('../views/welcome.vue')), 'Welcome');
const Report = r => require.ensure([], () => r(require('../views/report.vue')), 'Report');
const Feedback = r => require.ensure([], () => r(require('../views/feedback.vue')), 'Feedback');
const TopicList = r => require.ensure([], () => r(require('../views/topic/list.vue')), 'Topic');
const TopicCreate = r => require.ensure([], () => r(require('../views/topic/create.vue')), 'Topic');
const TopicEdit = r => require.ensure([], () => r(require('../views/topic/edit.vue')), 'Topic');
const LibraryEdit = r => require.ensure([], () => r(require('../views/library/edit.vue')), 'Library');
const LibraryCreate = r => require.ensure([], () => r(require('../views/library/create.vue')), 'Library');
const LibraryList = r => require.ensure([], () => r(require('../views/library/list.vue')), 'Library');
const LibraryView = r => require.ensure([], () => r(require('../views/library/list.vue')), 'Library');
const TopicCategory = r => require.ensure([], () => r(require('../views/category/list.vue')), 'TopicCategory');
const TopicCategoryCreate = r => require.ensure([], () => r(require('../views/category/create.vue')), 'TopicCategory');
const TopicCategoryEdit = r => require.ensure([], () => r(require('../views/category/edit.vue')), 'TopicCategory');
const RecommendList = r => require.ensure([], () => r(require('../views/recommend/list.vue')), 'Recommend');
const RecommendCreate = r => require.ensure([], () => r(require('../views/recommend/create.vue')), 'Recommend');
const QuestionReportList = r => require.ensure([], () => r(require('../views/questionReport/list.vue')), 'QuestionReport');
const QuestionReportEdit = r => require.ensure([], () => r(require('../views/questionReport/edit.vue')), 'QuestionReport');

const ReviewList = r => require.ensure([], () => r(require('../views/review/list.vue')), 'Review');

const Login = r => require.ensure([], () => r(require('../views/login.vue')), 'Login');
const NotFound = r => require.ensure([], () => r(require('../views/404.vue')), 'NotFound');

export default [
  {
    path: '/login',
    component: Login
  },
  {
    path: '/404',
    component: NotFound
  },
  {
    path: '/',
    component: Index,
    redirect: 'login',
    children: [
      {
        path: '/welcome',
        component: Welcome
      },
      {
        path: '/report',
        component: Report
      },
      {
        path: '/feedback',
        component: Feedback
      },
      {
        path: '/topicList',
        component: TopicList
      },
      {
        path: '/topicCreate',
        component: TopicCreate
      },
      {
        path: '/topicEdit/:id',
        name: 'topicEdit',
        component: TopicEdit
      },
      {
        path: '/topicCategory',
        component: TopicCategory
      },
      {
        path: '/topicCategoryCreate',
        component: TopicCategoryCreate
      },
      {
        path: '/topicCategoryEdit/:id',
        name: 'topicCategoryEdit',
        component: TopicCategoryEdit
      },
      {
        path: '/libraryEdit/:id',
        name: 'libraryEdit',
        component: LibraryEdit
      },
      {
        path: '/libraryCreate/:id',
        name: 'libraryCreate',
        component: LibraryCreate
      },
      {
        path: '/libraryList',
        name: 'libraryList',
        component: LibraryList
      },
      {
        path: '/libraryView/:id',
        name: 'libraryView',
        component: LibraryView
      },
      {
        path: '/recommendList',
        name: 'recommentList',
        component: RecommendList
      },
      {
        path: '/recommendCreate',
        name: 'recommentCreate',
        component: RecommendCreate
      },
      {
        path: '/questionReportList',
        name: 'questionReportList',
        component: QuestionReportList
      },
      {
        path: '/questionReportEdit/:id',
        name: 'questionReportEdit',
        component: QuestionReportEdit
      },
      {
        path: '/reviewList',
        name: 'reviewList',
        component: ReviewList
      },
      {
        path: '/logApp',
        component: LogApp
      },
      {
        path: '/logType',
        component: LogType
      },
      {
        path: '/logTable',
        component: LogTable
      }
    ]
  },
  {
    path: '*',
    redirect: '/404'
  }
];
