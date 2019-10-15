#define MAX_MACHINE_COUNT 5

void add_machine();
void show_all_machines();
void search_by_index();
void delete_machine();
void update_status();
void init_machines();
void write_to_file();
void read_from_file();

struct machine {
    int index, pin, status;
    char name[16], location[32];
};