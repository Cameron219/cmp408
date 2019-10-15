#include <stdio.h>
#include <stdbool.h>

#define STAR_PADDING 49

/**
 * Print defined amount of stars for menu.
 */
void print_star_padding() {
    for(int i = 0; i < STAR_PADDING; i++) {
        printf("*");
    }
    printf("\n");
}

/**
 * Print the menu
 */
void print_menu()
{
    print_star_padding();

    printf("* Vending Machine Control Console\t\t*\n");
    printf("*\t\tSubmitted By: Cameron McCallion\t*\n");
    printf("*\t\tStudent ID: 1702519\t\t*\n");
    printf("*\t\tAbertay University\t\t*\n");

    print_star_padding();

    printf("1. Add Machine\n");
    printf("2. Show All Machines\n");
    printf("3. Search By Index\n");
    printf("4. Delete Machine\n");
    printf("5. Update Status\n");
    printf("9. Exit\n");

    print_star_padding();
}

/**
 * Check validity of menu input choice
 */
bool isValidMenuChoice(int choice) {
    if((choice > 0 && choice < 6) || choice == 9) 
        return true;
    return false;
}

/**
 * Prompt the user for a menu input
 * Re-prompt until valid
 */
int get_menu_choice()
{
    char c;
    int choice = -1;


    // Ask user for input
    do {
        printf("Enter your choice: ");
        scanf("%c", &c);
        if(c == EOF) return -1; // If nothing in stdin
        if(c == '\n') continue; // Handles user not entering anything (just enter)
        if(getchar() != '\n') { // If there is not any other characters entered.
            while((c = getchar()) != '\n' && c != EOF); // Clear them out until reaches new line or EOF.
        } else {
            choice = c - '0'; // Convert character to digit
        }
        
    } while(!isValidMenuChoice(choice)); // Ask again if input is not valid (1,2,3,4,5,9)

    return choice;
}