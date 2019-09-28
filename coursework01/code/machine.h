#define MAX_MACHINE_COUNT 5

void add_machine();
void show_all_machines();
void search_by_index();
void delete_machine();
void update_status();

struct machine {
    int index, pin, status;
    char name[16], location[32];
};