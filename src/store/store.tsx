import { createStore, combineReducers, compose } from "redux";
import loggedReducer from '../store/UserLogged/reducer'
import userDataReducer from '../store/UserData/reducer'

const reducers = combineReducers({
    loggedReducer,userDataReducer
})
declare global {
    interface Window {
      __REDUX_DEVTOOLS_EXTENSION_COMPOSE__?: typeof compose;
    }
  }
  
  const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;
  const store = createStore(reducers, composeEnhancers())
// const store = createStore(reducers);
// const store = createStore(reducers, window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__());

export default store