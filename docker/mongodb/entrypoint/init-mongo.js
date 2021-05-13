db = db.getSiblingDB("telegram-db");

db.sessions.save( {
    chat_id: "123" , 
    first_name: "test",
    last_name: "test",
    message: "test"
});

db.sessions.remove( { 
    chat_id: "123" , 
    first_name: "test",
    last_name: "test",
    message: "test"
});