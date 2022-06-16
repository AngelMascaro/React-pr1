
interface User{
    logged:boolean,
    userId:string,
    userUsername:string
}

const initialState:User = {
    logged : false,
    userId:"",
    userUsername:""
}

export default (state = initialState, action)=>{
    if ( action.type === "USER_LOGIN"){
        return{
            ...state,
            logged : action.payload.logged,
            userId: action.payload.userid,
            userUsername: action.payload.username
        }
    }
    else return state
}