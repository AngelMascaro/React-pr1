
const SetUserData = ( username:string, email:string, src:string, birthday:string )=>{
    return{
        type: "SET_USER_DATA",
        payload:{
            Username : username,
            Email: email,
            Src: src,
            Birthday: birthday
        }
    }
}

export {SetUserData};