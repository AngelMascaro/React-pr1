
interface UserData{
    Username:string
    Email:string
    Src:string
    Birthday:string
}

const initialState:UserData = {
    Username:"",
    Email:"",
    Src:"",
    Birthday:""
}

export default (state = initialState, action)=>{
    if ( action.type === "SET_USER_DATA"){
        return{
            ...state,
            Username : action.payload.Username,
            Email: action.payload.Email,
            Src: action.payload.Src,
            Birthday: action.payload.Birthday
        }
    }
    else return state
}