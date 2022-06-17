
const SetUserData = ( username:string, email:string, src:string, birthday:string )=>{
// const SetUserData = ( data:object )=>{
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