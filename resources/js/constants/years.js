const years = () => {
    let count = 12;
    let previous = [...Array(count)].map((a, b) => {
        return `${new Date().getFullYear() - b - 1}-${new Date().getFullYear() -
            b}`;
    });
    let upcoming = [...Array(count)].map((a, b) => {
        return `${new Date().getFullYear() + b}-${new Date().getFullYear() +
            b +
            1}`;
    });
    return [...previous.reverse(), ...upcoming];
};

export default years;
