
const Login = ( logged:boolean, userid:string, username:string )=>{
    return{
        type: "USER_LOGIN",
        payload:{
            logged:logged,
            userid:userid,
            username: username
        }
    }
}

export {Login};