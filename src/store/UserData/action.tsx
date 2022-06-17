
const SetUserData = ( username:string, email:string, src:string, birthday:string )=>{
// const SetUserData = ( data:object )=>{
    return{
        type: "INSERT_USER_BDD",
        payload:{
            Username : username,
            Email: email,
            Src: src,
            Birthday: birthday
        }
    }
}

export {SetUserData};